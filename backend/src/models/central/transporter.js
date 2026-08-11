const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('transporter', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    transport_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    transport_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    transport_from: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    vehicle_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    gr_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    weight: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    freight: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    upload: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    updated: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    datefrom: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'transporter',
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
