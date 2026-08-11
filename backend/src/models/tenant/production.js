const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('production', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    po_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    machine_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    item_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    manpower_day: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    manpower_night: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contract_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    productprocess_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    remark: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    production_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    production_shift_a: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    production_shift_b: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    scrap: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    reading8am: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    reading8pm: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    nextday8am: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('O','C'),
      allowNull: true,
      defaultValue: "O"
    },
    plan_qty: {
      type: DataTypes.STRING(45),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'production',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
