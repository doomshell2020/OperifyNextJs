const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('contracts', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    supplier_id: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    workorder: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    cost: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    },
    contract_start_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    contract_end_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    added_time: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    updated_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    issuedate: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    operation_cost: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    labour_cost: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'contracts',
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
